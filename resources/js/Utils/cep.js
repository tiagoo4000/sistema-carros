// Utilitários de CEP compartilhados
export const normalizeDigits = (s) => (s || '').replace(/\D/g, '');

export const maskCEP = (val) => {
  let v = normalizeDigits(val).slice(0, 8);
  return v.replace(/(\d{5})(\d{3})/, '$1-$2');
};

export const lookupCEP = async (cep) => {
  const digits = normalizeDigits(cep);
  if (!digits || digits.length !== 8) {
    return { erro: true, mensagem: 'CEP inválido' };
  }
  const resp = await fetch(`https://viacep.com.br/ws/${digits}/json/`);
  if (!resp.ok) {
    return { erro: true, mensagem: 'Erro ao consultar CEP' };
  }
  const data = await resp.json();
  if (data.erro) {
    return { erro: true, mensagem: 'CEP não encontrado' };
  }
  return {
    erro: false,
    logradouro: data.logradouro || '',
    bairro: data.bairro || '',
    localidade: data.localidade || '',
    uf: data.uf || '',
  };
};
